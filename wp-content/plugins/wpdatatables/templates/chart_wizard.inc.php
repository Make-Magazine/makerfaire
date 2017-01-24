<?php if( isset( $chartObj ) ) { ?>
    <script type='text/javascript'>var editing_chart_data= {render_data: <?php echo json_encode( $chartObj->getRenderData() ); ?>, highcharts_render_data: <?php echo json_encode( $chartObj->getHighchartsRenderData() ); ?>, engine: "<?php echo $chartObj->getEngine();?>", type: "<?php echo $chartObj->getType(); ?>", selected_columns: <?php echo json_encode( $chartObj->getSelectedColumns() ) ?>, range_type: "<?php echo $chartObj->getRangeType() ?>"<?php if( $chartObj->getRangeType() == 'picked_range' ){ ?>, row_range: <?php echo json_encode( $chartObj->getRowRange() ); } ?>, title: "<?php echo $chartObj->getTitle(); ?>", follow_filtering: <?php echo (int) $chartObj->getFollowFiltering(); ?>, wpdatatable_id: <?php echo $chartObj->getwpDataTableId(); ?>  };</script>
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
                                                                <div class="strings_error" style="display:none"><?php _e( 'Please do not add more then one string-type (date/time, image, email, URL) column since only one can be used as a label','wpdatatables' ); ?></div>
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
                                            <!--CHART-->
                                            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                                <div class="postbox">
                                                    <div class="handlediv" title="Click to toggle" aria-expanded="true"><br></div>
                                                    <h3 class="hndle ui-sortable-handle" style="height: 27px">
                                                        <span><div class="dashicons dashicons-chart-line"></div><?php _e(' Chart','wpdatatables');?></span>
                                                    </h3>
                                                    <div class="inside">
                                                        <table class="form-table wpDataTables">
                                                            <tbody>
                                                            <tr>
                                                                <td style="vertical-align: top; width: 250px;">
                                                                    <label for="chart_width"><span><strong><?php _e('Chart width','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The width of the chart','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" id="chart_width" value="400" style="margin-bottom: 5px" /><br/>
                                                                    <input type="checkbox" id="chart_responsive_width" /> <?php _e('Responsive width','wpdatatables');?><br/>
                                                                    <span class="description"><small><?php _e('If you tick this chart width will always adjust to 100% width of the container','wpdatatables');?>.</small></span>
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
                                                                <td style="vertical-align: top; width: 250px;">
                                                                    <label for="group_chart"><span><strong><?php _e('Group chart','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('If you tick this checkbox, the values of the rows with same label will be summed up and rendered as a single series. If you leave it unticked all rows will be rendered as separate series','wpdatatables');?></small>.</span>

                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="group_chart" /> <label for="group_chart"><?php _e('Enable grouping','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr id="background_color_row">
                                                                <td>
                                                                    <label for="background_color"><span><strong><?php _e('Background color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The background color for the outer chart area','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="background_color_container">
                                                                        <input type ="text" class="background_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr id="border_width_row">
                                                                <td>
                                                                    <label for="border_width"><span><strong><?php _e('Border width','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The pixel width of the outer chart border','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="border_width" id="border_width" value="0">
                                                                </td>
                                                            </tr>
                                                            <tr id="border_color_row">
                                                                <td>
                                                                    <label for="border_color"><span><strong><?php _e('Border color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The color of the outer chart border','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="border_color_container">
                                                                        <input type ="text" class="border_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr id="border_radius_row">
                                                                <td>
                                                                    <label for="border_radius"><span><strong><?php _e('Border radius','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The corner radius of the outer chart border','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="border_radius" id="border_radius" value="0">
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts" id="zoom_type_row">
                                                                <td>
                                                                    <label for="zoom_type"><span><strong><?php _e('Zoom type','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Decides in what dimensions the user can zoom by dragging the mouse','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="zoom_type" id="zoom_type">
                                                                        <option selected="selected" value="none">None</option>
                                                                        <option value="x">X</option>
                                                                        <option value="y">Y</option>
                                                                        <option value="xy">XY</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts" id="panning_row">
                                                                <td>
                                                                    <label for="panning"><span><strong><?php _e('Panning','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Allow panning in a chart. Best used with <b>panKey</b> to combine zooming and panning','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="panning"/> <label for="panning"><?php _e('Enable panning','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts" id="pan_key_row">
                                                                <td>
                                                                    <label for="pan_key"><span><strong><?php _e('Pan key','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Allows setting a key to switch between zooming and panning','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="pan_key" id="pan_key">
                                                                        <option selected="selected" value="shift">Shift</option>
                                                                        <option value="ctrl">Ctrl</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr id="plot_background_color_row">
                                                                <td>
                                                                    <label for="plot_background_color"><span><strong><?php _e('Plot background color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The background color or gradient for the plot area','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="plot_background_color_container">
                                                                        <input type ="text" class="plot_background_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="plot_background_image"><span><strong><?php _e('Plot background image','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The URL for an image to use as the plot background','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="plot_background_image" id="plot_background_image" value="">
                                                                </td>
                                                            </tr>
                                                            <tr id="plot_border_width_row">
                                                                <td>
                                                                    <label for="plot_border_width"><span><strong><?php _e('Plot border width','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The pixel width of the plot area border','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="plot_border_width" id="plot_border_width" value="0">
                                                                </td>
                                                            </tr>
                                                            <tr id="plot_border_color_row">
                                                                <td>
                                                                    <label for="plot_border_color"><span><strong><?php _e('Plot border color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The color of the inner chart or plot area border','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="plot_border_color_container">
                                                                        <input type ="text" class="plot_border_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="google" id="font_size_row">
                                                                <td>
                                                                    <label for="font_size"><span><strong><?php _e('Font size','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The default font size, in pixels, of all text in the chart','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="font_size" id="font_size">
                                                                </td>
                                                            </tr>
                                                            <tr class="google" id="font_name_row">
                                                                <td>
                                                                    <label for="font_name"><span><strong><?php _e('Font name','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The default font face for all text in the chart','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="font_name" id="font_name" value="Arial">
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--CHART END-->
                                            <!--SERIES-->
                                            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                                <div class="postbox closed">
                                                    <div class="handlediv" title="Click to toggle" aria-expanded="true"><br></div>
                                                    <h3 class="hndle ui-sortable-handle" style="height: 27px">
                                                        <span><div class="dashicons dashicons-randomize"></div><?php _e(' Series','wpdatatables');?></span>
                                                    </h3>
                                                    <div class="inside">
                                                        <table class="form-table wpDataTables">
                                                            <tbody>
                                                            <tr>
                                                                <td style= "width: 250px;">
                                                                    <label for="series_settings_container"><span><strong><?php _e('Series settings','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('If you want to redefine the series labels and colors you can do it here','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="series_settings_container">

                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="google" id="curve_type_row">
                                                                <td>
                                                                    <label for="curve_type"><span><strong><?php _e('Curve type','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Controls the curve of the lines','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="curve_type"/> <label for="curve_type"><?php _e('Check for smoothed lines','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="google" id="three_d_row">
                                                                <td>
                                                                    <label for="three_d"><span><strong><?php _e('3D','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('If true, displays a three-dimensional chart','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="three_d"/> <label for="three_d"><?php _e('Check for 3D pie chart','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--SERIES END-->
                                            <!--AXES-->
                                            <div id="normal-sortables" class="meta-box-sortables ui-sortable axes">
                                                <div class="postbox closed">
                                                    <div class="handlediv" title="Click to toggle" aria-expanded="true"><br></div>
                                                    <h3 class="hndle ui-sortable-handle" style="height: 27px">
                                                        <span><div class="dashicons dashicons-arrow-right-alt"></div><?php _e(' Axes','wpdatatables');?></span><br/>
                                                    </h3>
                                                    <div class="inside">
                                                        <table class="form-table wpDataTables">
                                                            <tbody>
                                                            <tr id="show_grid_row">
                                                                <td style="vertical-align: top; width: 250px;">
                                                                    <label for="show_grid"><span><strong><?php _e('Grid','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Do you want to show grid on the chart','wpdatatables');?>?</small></span>

                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="show_grid" checked="checked" /> <label for="show_grid"><?php _e('Show grid','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="highcharts_line_dash_style"><span><strong><?php _e('Grid line style','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The dash or dot style of the grid lines','wpdatatables');?></small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="highcharts_line_dash_style" id="highcharts_line_dash_style">
                                                                        <option selected="selected" value="solid">Solid</option>
                                                                        <option value="shortdash">Short Dash</option>
                                                                        <option value="shortdot">Short Dot</option>
                                                                        <option value="shortdashdot">Short Dash Dot</option>
                                                                        <option value="shortdashdotdot">Short Dash Dot Dot</option>
                                                                        <option value="dot">Dot</option>
                                                                        <option value="dash">Dash</option>
                                                                        <option value="longdash">Long Dash</option>
                                                                        <option value="dashdot">Dash Dot</option>
                                                                        <option value="dongdashdot">Long Dash Dot</option>
                                                                        <option value="longdashdotdot">Long Dash Dot Dot</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr id="horizontal_axis_label_row">
                                                                <td>
                                                                    <label for="horizontal_axis_label"><span><strong><?php _e('Horizontal axis label','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Name of the horizontal axis','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" id="horizontal_axis_label" value="" />
                                                                </td>
                                                            </tr>
                                                            <tr id="horizontal_axis_crosshair_row">
                                                                <td>
                                                                    <label for="horizontal_axis_crosshair"><span><strong><?php _e('Horizontal crosshair','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Configure a horizontal crosshair that follows either the mouse pointer or the hovered point lines','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="horizontal_axis_crosshair" /> <label for="horizontal_axis_crosshair"><?php _e('Show x-Axis crosshair','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="google" id = "horizontal_axis_direction_row">
                                                                <td>
                                                                    <label for="horizontal_axis_direction"><span><strong><?php _e('Horizontal axis direction','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The direction in which the values along the horizontal axis grow. Specify -1 to reverse the order of the values','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="horizontal_axis_direction" id="horizontal_axis_direction">
                                                                        <option selected="selected" value="1">1</option>
                                                                        <option value="-1">-1</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr id="vertical_axis_label_row">
                                                                <td>
                                                                    <label for="vertical_axis_label"><span><strong><?php _e('Vertical axis label','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Name of the vertical axis','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" id="vertical_axis_label" value="" />
                                                                </td>
                                                            </tr>
                                                            <tr id="vertical_axis_crosshair_row">
                                                                <td>
                                                                    <label for="vertical_axis_crosshair"><span><strong><?php _e('Vertical crosshair','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Configure a vertical crosshair that follows either the mouse pointer or the hovered point lines','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="vertical_axis_crosshair" /> <label for="vertical_axis_crosshair"><?php _e('Show y-Axis crosshair','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="google" id="vertical_axis_direction_row">
                                                                <td>
                                                                    <label for="vertical_axis_direction"><span><strong><?php _e('Vertical axis direction','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The direction in which the values along the vertical axis grow. Specify -1 to reverse the order of the values','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="vertical_axis_direction" id="vertical_axis_direction">
                                                                        <option selected="selected" value="1">1</option>
                                                                        <option value="-1">-1</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr id="vertical_axis_min_row">
                                                                <td>
                                                                    <label for="vertical_axis_min"><span><strong><?php _e('Vertical axis min value','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The minimum value of the axis','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" id="vertical_axis_min" value="" />
                                                                </td>
                                                            </tr>
                                                            <tr id="vertical_axis_max_row">
                                                                <td>
                                                                    <label for="vertical_axis_max"><span><strong><?php _e('Vertical axis max value','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The maximum value of the axis','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" id="vertical_axis_max" value="" />
                                                                </td>
                                                            </tr>
                                                            <tr id="inverted_row">
                                                                <td>
                                                                    <label for="inverted"><span><strong><?php _e('Invert','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Whether to invert the axes so that the x axis is vertical and y axis is horizontal','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="inverted"/> <label for="inverted"><?php _e('Invert chart axes','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--AXES END-->
                                            <!--TITLE-->
                                            <div id="normal-sortables" class="meta-box-sortables ui-sortable title">
                                                <div class="postbox closed">
                                                    <div class="handlediv" title="Click to toggle" aria-expanded="true"><br></div>
                                                    <h3 class="hndle ui-sortable-handle" style="height: 27px">
                                                        <span><div class="dashicons dashicons-editor-textcolor"></div><?php _e(' Title','wpdatatables');?></span>
                                                    </h3>
                                                    <div class="inside">
                                                        <table class="form-table wpDataTables">
                                                            <tbody>
                                                            <tr id="show_chart_title_row">
                                                                <td style="vertical-align: top; width: 250px;">
                                                                    <label for="show_chart_title"><span><strong><?php _e('Chart title','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Do you want to show the chart title on the page','wpdatatables');?>?</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="show_chart_title" checked="checked" /> <label for="show_chart_title"><?php _e('Show chart title on the page','wpdatatables'); ?></label><br/>
                                                                </td>
                                                            </tr>
                                                            <tr id="title_floating_row">
                                                                <td>
                                                                    <label for="title_floating"><span><strong><?php _e('Title floating','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('When the title is floating, the plot area will not move to make space for it','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="title_floating"/> <label for="title_floating"><?php _e('Enable floating','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="title_align"><span><strong><?php _e('Title align','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The horizontal alignment of the title','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="title_align" id="title_align">
                                                                        <option selected="selected" value="center">Center</option>
                                                                        <option value="left">Left</option>
                                                                        <option value="right">Right</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="subtitle"><span><strong><?php _e('Subtitle','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The chart\'s subtitle','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="subtitle" id="subtitle">
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="subtitle_align"><span><strong><?php _e('Subtitle align','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The horizontal alignment of the subtitle','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="subtitle_align" id="subtitle_align">
                                                                        <option selected="selected" value="center">Center</option>
                                                                        <option value="left">Left</option>
                                                                        <option value="right">Right</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--TITLE END-->
                                            <!--TOOLTIP-->
                                            <div id="normal-sortables" class="meta-box-sortables ui-sortable tooltip">
                                                <div class="postbox closed">
                                                    <div class="handlediv" title="Click to toggle" aria-expanded="true"><br></div>
                                                    <h3 class="hndle ui-sortable-handle" style="height: 27px">
                                                        <span><div class="dashicons dashicons-testimonial"></div><?php _e(' Tooltip','wpdatatables');?></span>
                                                    </h3>
                                                    <div class="inside">
                                                        <table class="form-table wpDataTables">
                                                            <tbody>
                                                            <tr id="tooltip_enabled_row">
                                                                <td style="vertical-align: top; width: 250px;">
                                                                    <label for="tooltip_enabled"><span><strong><?php _e('Tooltip','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Enable or disable the tooltip','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="tooltip_enabled" checked="checked" /> <label for="show_grid"><?php _e('Show tooltip','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="tooltip_background_color"><span><strong><?php _e('Background color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The background color for the tooltip','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="tooltip_background_color_container">
                                                                        <input type ="text" class="tooltip_background_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="tooltip_border_width"><span><strong><?php _e('Border width','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The pixel width of the tooltip border','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="tooltip_border_width" id="tooltip_border_width" value="1">
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="tooltip_border_color"><span><strong><?php _e('Border color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The color of the tooltip border','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="tooltip_border_color_container">
                                                                        <input type ="text" class="tooltip_border_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="tooltip_border_radius"><span><strong><?php _e('Border radius','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The radius of the rounded border corners','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="tooltip_border_radius" id="tooltip_border_radius" value="3">
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="tooltip_shared"><span><strong><?php _e('Shared tooltip','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('When the tooltip is shared, the entire plot area will capture mouse movement or touch events','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="tooltip_shared"  /> <label for="tooltip_shared"><?php _e('Share tooltip','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="tooltip_value_prefix"><span><strong><?php _e('Value prefix','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('A string to prepend to each series\' y value','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="tooltip_value_prefix" id="tooltip_value_prefix">
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="tooltip_value_suffix"><span><strong><?php _e('Value suffix','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('A string to append to each series\' y value','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="tooltip_value_suffix" id="tooltip_value_suffix">
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--TOOLTIP END-->
                                            <!--LEGEND-->
                                            <div id="normal-sortables" class="meta-box-sortables ui-sortable legend">
                                                <div class="postbox closed">
                                                    <div class="handlediv" title="Click to toggle" aria-expanded="true"><br></div>
                                                    <h3 class="hndle ui-sortable-handle" style="height: 27px">
                                                        <span><div class="dashicons dashicons-editor-ul"></div><?php _e(' Legend','wpdatatables');?></span>
                                                    </h3>
                                                    <div class="inside">
                                                        <table class="form-table wpDataTables">
                                                            <tbody>
                                                            <tr class="highcharts">
                                                                <td style="vertical-align: top; width: 250px;">
                                                                    <label for="show_legend"><span><strong><?php _e('Legend','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Enable or disable the legend','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="show_legend" checked="checked" /> <label for="show_legend"><?php _e('Show legend','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr class="google" id="legend_position_row">
                                                                <td>
                                                                    <label for="legend_position"><span><strong><?php _e('Position','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Position of the legend','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="legend_position" id="legend_position">
                                                                        <option selected="selected" value="right">Right</option>
                                                                        <option value="bottom">Bottom</option>
                                                                        <option value="top">Top</option>
                                                                        <option value="in">In</option>
                                                                        <option value="none">None</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="legend_background_color"><span><strong><?php _e('Background color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The background color of the legend','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="legend_background_color_container">
                                                                        <input type ="text" class="legend_background_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="legend_title"><span><strong><?php _e('Title','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('A title to be added on top of the legend','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="legend_title" id="legend_title">
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="legend_layout"><span><strong><?php _e('Layout','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The layout of the legend items','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="legend_layout" id="legend_layout">
                                                                        <option selected="selected" value="horizontal">Horizontal</option>
                                                                        <option value="vertical">Vertical</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="legend_align"><span><strong><?php _e('Align','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The horizontal alignment of the legend box within the chart area','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="legend_align" id="legend_align">
                                                                        <option selected="selected" value="center">Center</option>
                                                                        <option value="left">Left</option>
                                                                        <option value="right">Right</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr id="legend_vertical_align_row">
                                                                <td>
                                                                    <label for="legend_vertical_align"><span><strong><?php _e('Vertical align','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The vertical alignment of the legend box','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="legend_vertical_align" id="legend_vertical_align">
                                                                        <option selected="selected" value="bottom">Bottom</option>
                                                                        <option value="middle">Middle</option>
                                                                        <option value="top">Top</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="legend_border_width"><span><strong><?php _e('Border width','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The width of the drawn border around the legend','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="legend_border_width" id="legend_border_width" value="0">
                                                                </td>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="legend_border_color"><span><strong><?php _e('Border color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The color of the drawn border around the legend','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="legend_border_color_container">
                                                                        <input type ="text" class="legend_border_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            </tr>
                                                            <tr class="highcharts">
                                                                <td>
                                                                    <label for="legend_border_radius"><span><strong><?php _e('Border radius','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The border corner radius of the legend','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="legend_border_radius" id="legend_border_radius" value="0">
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--LEGEND END-->
                                            <!--EXPORTING-->
                                            <div id="normal-sortables" class="meta-box-sortables ui-sortable highcharts">
                                                <div class="postbox closed">
                                                    <div class="handlediv" title="Click to toggle" aria-expanded="true"><br></div>
                                                    <h3 class="hndle ui-sortable-handle" style="height: 27px">
                                                        <span><div class="dashicons dashicons-format-image"></div><?php _e(' Exporting','wpdatatables');?></span>
                                                    </h3>
                                                    <div class="inside">
                                                        <table class="form-table wpDataTables">
                                                            <tbody>
                                                            <tr>
                                                                <td style="vertical-align: top; width: 250px;">
                                                                    <label for="exporting"><span><strong><?php _e('Exporting','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Whether to enable the exporting module','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="exporting" checked="checked" /><label for="exporting"><?php _e('Export chart','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="exporting_data_labels"><span><strong><?php _e('Data labels','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Add data labels to improve readaility of the exported chart','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="exporting_data_labels"/> <label for="exporting_data_labels"><?php _e('Show data labels','wpdatatables'); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="exporting_file_name"><span><strong><?php _e('File name','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The filename, without extension, to use for the exported chart','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="exporting_file_name" id="exporting_file_name">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="exporting_width"><span><strong><?php _e('Width','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The width of the original chart when exported','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="exporting_width" id="exporting_width">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="exporting_button_align"><span><strong><?php _e('Button align','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Alignment for the export button','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="exporting_button_align" id="exporting_button_align">
                                                                        <option selected="selected" value="right">Right</option>
                                                                        <option value="center">Center</option>
                                                                        <option value="left">Left</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="exporting_button_vertical_align"><span><strong><?php _e('Button vertical align','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The vertical alignment of the export button','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <select name="exporting_button_vertical_align" id="exporting_button_vertical_align">
                                                                        <option selected="selected" value="top">Top</option>
                                                                        <option value="middle">Middle</option>
                                                                        <option value="bottom">Bottom</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="exporting_button_color"><span><strong><?php _e('Button color','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The color of the symbol\'s stroke or line','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <div id="exporting_button_color_container">
                                                                        <input type ="text" class="exporting_button_color">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="exporting_button_text"><span><strong><?php _e('Button text','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('A text string to add to the individual button','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="exporting_button_text" id="exporting_button_text">
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--EXPORTING END-->
                                            <!--CREDITS-->
                                            <div id="normal-sortables" class="meta-box-sortables ui-sortable highcharts">
                                                <div class="postbox closed">
                                                    <div class="handlediv" title="Click to toggle" aria-expanded="true"><br></div>
                                                    <h3 class="hndle ui-sortable-handle" style="height: 27px">
                                                        <span><div class="dashicons dashicons-warning"></div><?php _e(' Credits','wpdatatables');?></span>
                                                    </h3>
                                                    <div class="inside">
                                                        <table class="form-table wpDataTables">
                                                            <tbody>
                                                            <tr>
                                                                <td>
                                                                    <label for="credits"><span><strong><?php _e('Credits','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Whether to show the credits text','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" id="credits" checked="checked" /> <label for="credits"><?php _e('Show credits','wpdatatables'); ?></label><br/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="credits_href"><span><strong><?php _e('Credits href','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The URL for the credits label','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" id="credits_href" value="http://www.highcharts.com"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label for="credits_text"><span><strong><?php _e('Credits text','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('The text for the credits label','wpdatatables');?>.</small></span>
                                                                </td>
                                                                <td>
                                                                    <input type="text" id="credits_text" value="Highcharts.com"/>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--CREDITS END-->
                                        </fieldset>
                                    </div>
                                    <div class="chartWizardStep step5" data-step="step5" style="display: none">
                                        <fieldset style="margin: 10px;">
                                            <table style="width: 100%">
                                                <tr>
                                                    <td colspan="2">
                                                        <div id="googleChartContainer" style="width: 100%; max-width: 700px">
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