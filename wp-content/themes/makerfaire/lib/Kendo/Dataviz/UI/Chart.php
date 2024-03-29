<?php

namespace Kendo\Dataviz\UI;

class Chart extends \Kendo\UI\Widget {
    protected function name() {
        return 'Chart';
    }
//>> Properties

    /**
    * If set to false the widget will not bind to the data source during initialization. In this case data binding will occur when the change event of the data source is fired. By default the widget will bind to the data source specified in the configuration.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * The default options for all chart axes. Accepts the options supported by categoryAxis, valueAxis, xAxis and yAxis.
    * @param \Kendo\Dataviz\UI\ChartAxisDefaults|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function axisDefaults($value) {
        return $this->setProperty('axisDefaults', $value);
    }

    /**
    * Adds ChartCategoryAxisItem to the Chart.
    * @param \Kendo\Dataviz\UI\ChartCategoryAxisItem|array,... $value one or more ChartCategoryAxisItem to add.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function addCategoryAxisItem($value) {
        return $this->add('categoryAxis', func_get_args());
    }

    /**
    * The chart area configuration options. Represents the entire visible area of the chart.
    * @param \Kendo\Dataviz\UI\ChartArea|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function chartArea($value) {
        return $this->setProperty('chartArea', $value);
    }

    /**
    * Sets the data source of the Chart.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * The chart legend configuration options.
    * @param \Kendo\Dataviz\UI\ChartLegend|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function legend($value) {
        return $this->setProperty('legend', $value);
    }

    /**
    * Adds ChartPane to the Chart.
    * @param \Kendo\Dataviz\UI\ChartPane|array,... $value one or more ChartPane to add.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function addPane($value) {
        return $this->add('panes', func_get_args());
    }

    /**
    * Specifies if the chart can be panned.
    * @param boolean|\Kendo\Dataviz\UI\ChartPannable|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function pannable($value) {
        return $this->setProperty('pannable', $value);
    }

    /**
    * Configures the export settings for the saveAsPDF method.
    * @param \Kendo\Dataviz\UI\ChartPdf|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function pdf($value) {
        return $this->setProperty('pdf', $value);
    }

    /**
    * Specifies if the series visible option should be persisted when changing the dataSource data.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function persistSeriesVisibility($value) {
        return $this->setProperty('persistSeriesVisibility', $value);
    }

    /**
    * The plot area configuration options. The plot area is the area which displays the series.
    * @param \Kendo\Dataviz\UI\ChartPlotArea|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function plotArea($value) {
        return $this->setProperty('plotArea', $value);
    }

    /**
    * Sets the preferred rendering engine. If it is not supported by the browser, the Chart will switch to the first available mode.The supported values are: "svg" - renders the widget as inline SVG document, if available or "canvas" - renders the widget as a Canvas element, if available..
    * @param string $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function renderAs($value) {
        return $this->setProperty('renderAs', $value);
    }

    /**
    * Adds ChartSeriesItem to the Chart.
    * @param \Kendo\Dataviz\UI\ChartSeriesItem|array,... $value one or more ChartSeriesItem to add.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function addSeriesItem($value) {
        return $this->add('series', func_get_args());
    }

    /**
    * The default colors for the chart's series. When all colors are used, new colors are pulled from the start again.
    * @param array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function seriesColors($value) {
        return $this->setProperty('seriesColors', $value);
    }

    /**
    * The default options for all series.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaults|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function seriesDefaults($value) {
        return $this->setProperty('seriesDefaults', $value);
    }

    /**
    * The chart theme. This can be either a built-in theme or "sass". When set to "sass" the chart will read the variables from the Sass-based themes. More information on the built-in themes could be found in the Less-based themes article.The supported values are: "sass" - special value, see notes; "black"; "blueopal"; "bootstrap"; "default"; "highcontrast"; "metro"; "metroblack"; "moonlight"; "silver" or "uniform".
    * @param string $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function theme($value) {
        return $this->setProperty('theme', $value);
    }

    /**
    * The chart title configuration options or text.
    * @param string|\Kendo\Dataviz\UI\ChartTitle|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function title($value) {
        return $this->setProperty('title', $value);
    }

    /**
    * The chart series tooltip configuration options.
    * @param \Kendo\Dataviz\UI\ChartTooltip|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function tooltip($value) {
        return $this->setProperty('tooltip', $value);
    }

    /**
    * If set to true the chart will play animations when displaying the series. By default animations are enabled.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function transitions($value) {
        return $this->setProperty('transitions', $value);
    }

    /**
    * Adds ChartValueAxisItem to the Chart.
    * @param \Kendo\Dataviz\UI\ChartValueAxisItem|array,... $value one or more ChartValueAxisItem to add.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function addValueAxisItem($value) {
        return $this->add('valueAxis', func_get_args());
    }

    /**
    * Adds ChartXAxisItem to the Chart.
    * @param \Kendo\Dataviz\UI\ChartXAxisItem|array,... $value one or more ChartXAxisItem to add.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function addXAxisItem($value) {
        return $this->add('xAxis', func_get_args());
    }

    /**
    * Adds ChartYAxisItem to the Chart.
    * @param \Kendo\Dataviz\UI\ChartYAxisItem|array,... $value one or more ChartYAxisItem to add.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function addYAxisItem($value) {
        return $this->add('yAxis', func_get_args());
    }

    /**
    * Specifies if the chart can be zoomed.
    * @param boolean|\Kendo\Dataviz\UI\ChartZoomable|array $value
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function zoomable($value) {
        return $this->setProperty('zoomable', $value);
    }

    /**
    * Sets the axisLabelClick event of the Chart.
    * Fired when the user clicks an axis label.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function axisLabelClick($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('axisLabelClick', $value);
    }

    /**
    * Sets the dataBound event of the Chart.
    * Fired when the widget is bound to data from its data source.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the drag event of the Chart.
    * Fired as long as the user is dragging the chart using the mouse or swipe gestures.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function drag($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('drag', $value);
    }

    /**
    * Sets the dragEnd event of the Chart.
    * Fired when the user stops dragging the chart.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function dragEnd($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragEnd', $value);
    }

    /**
    * Sets the dragStart event of the Chart.
    * Fired when the user starts dragging the chart.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function dragStart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragStart', $value);
    }

    /**
    * Sets the legendItemClick event of the Chart.
    * Fires when an legend item is clicked, before the selected series visibility is toggled. Can be cancelled.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function legendItemClick($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('legendItemClick', $value);
    }

    /**
    * Sets the legendItemHover event of the Chart.
    * Fires when an legend item is hovered.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function legendItemHover($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('legendItemHover', $value);
    }

    /**
    * Sets the legendItemLeave event of the Chart.
    * Fires when the cursor leaves a legend item.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function legendItemLeave($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('legendItemLeave', $value);
    }

    /**
    * Sets the noteClick event of the Chart.
    * Fired when the user clicks one of the notes.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function noteClick($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('noteClick', $value);
    }

    /**
    * Sets the noteHover event of the Chart.
    * Fired when the user hovers one of the notes.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function noteHover($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('noteHover', $value);
    }

    /**
    * Sets the noteLeave event of the Chart.
    * Fired when the cursor leaves a note.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function noteLeave($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('noteLeave', $value);
    }

    /**
    * Sets the paneRender event of the Chart.
    * Fires when a pane is rendered because the chart is rendered, or the chart performs panning or zooming, or because the chart is exported with different options. The event can be used to render custom visuals in the panes.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function paneRender($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('paneRender', $value);
    }

    /**
    * Sets the plotAreaClick event of the Chart.
    * Fired when the user clicks the plot area.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function plotAreaClick($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('plotAreaClick', $value);
    }

    /**
    * Sets the plotAreaHover event of the Chart.
    * Fired when the user hovers the plot area.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function plotAreaHover($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('plotAreaHover', $value);
    }

    /**
    * Sets the plotAreaLeave event of the Chart.
    * Fired when the cursor leaves the plotArea.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function plotAreaLeave($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('plotAreaLeave', $value);
    }

    /**
    * Sets the render event of the Chart.
    * Fired when the chart is ready to render on screen.Can be used, for example, to remove loading indicators.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function renderEvent($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('render', $value);
    }

    /**
    * Sets the select event of the Chart.
    * Fired when the user modifies the selection.The range units are: Generic axis - Category index (0-based) or Date axis - Date instance. The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }

    /**
    * Sets the selectEnd event of the Chart.
    * Fired when the user completes modifying the selection.The range units are: Generic axis - Category index (0-based) or Date axis - Date instance. The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function selectEnd($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('selectEnd', $value);
    }

    /**
    * Sets the selectStart event of the Chart.
    * Fired when the user starts modifying the axis selection.The range units are: Generic axis - Category index (0-based) or Date axis - Date instance. The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function selectStart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('selectStart', $value);
    }

    /**
    * Sets the seriesClick event of the Chart.
    * Fired when the user clicks the chart series.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function seriesClick($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('seriesClick', $value);
    }

    /**
    * Sets the seriesHover event of the Chart.
    * Fired when the user hovers the chart series.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function seriesHover($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('seriesHover', $value);
    }

    /**
    * Sets the seriesOver event of the Chart.
    * Fired when the cursor is over the chart series.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function seriesOver($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('seriesOver', $value);
    }

    /**
    * Sets the seriesLeave event of the Chart.
    * Fired when the cursor leaves a chart series.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function seriesLeave($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('seriesLeave', $value);
    }

    /**
    * Sets the zoom event of the Chart.
    * Fired as long as the user is zooming the chart using the mousewheel.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function zoom($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('zoom', $value);
    }

    /**
    * Sets the zoomEnd event of the Chart.
    * Fired when the user stops zooming the chart.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function zoomEnd($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('zoomEnd', $value);
    }

    /**
    * Sets the zoomStart event of the Chart.
    * Fired when the user uses the mousewheel to zoom the chart.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Chart
    */
    public function zoomStart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('zoomStart', $value);
    }


//<< Properties
}

?>
