<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesDefaults extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The area chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function area($value) {
        return $this->setProperty('area', $value);
    }

    /**
    * The bar chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function bar($value) {
        return $this->setProperty('bar', $value);
    }

    /**
    * The border of the series.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsBorder|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function border($value) {
        return $this->setProperty('border', $value);
    }

    /**
    * The bubble chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function bubble($value) {
        return $this->setProperty('bubble', $value);
    }

    /**
    * The candlestick chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function candlestick($value) {
        return $this->setProperty('candlestick', $value);
    }

    /**
    * The column chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function column($value) {
        return $this->setProperty('column', $value);
    }

    /**
    * The donut chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function donut($value) {
        return $this->setProperty('donut', $value);
    }

    /**
    * The distance between category clusters.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function gap($value) {
        return $this->setProperty('gap', $value);
    }

    /**
    * The chart series label configuration.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsLabels|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function labels($value) {
        return $this->setProperty('labels', $value);
    }

    /**
    * The line chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function line($value) {
        return $this->setProperty('line', $value);
    }

    /**
    * The ohlc chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function ohlc($value) {
        return $this->setProperty('ohlc', $value);
    }

    /**
    * The chart series overlay options.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsOverlay|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function overlay($value) {
        return $this->setProperty('overlay', $value);
    }

    /**
    * The pie chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function pie($value) {
        return $this->setProperty('pie', $value);
    }

    /**
    * The range area chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function rangeArea($value) {
        return $this->setProperty('rangeArea', $value);
    }

    /**
    * The scatter chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function scatter($value) {
        return $this->setProperty('scatter', $value);
    }

    /**
    * The scatterLine chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function scatterLine($value) {
        return $this->setProperty('scatterLine', $value);
    }

    /**
    * The space between the chart series as proportion of the series width.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function spacing($value) {
        return $this->setProperty('spacing', $value);
    }

    /**
    * A boolean value indicating if the series should be stacked.
    * @param boolean|\Kendo\Dataviz\UI\ChartSeriesDefaultsStack|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function stack($value) {
        return $this->setProperty('stack', $value);
    }

    /**
    * The default type of the series.The supported values are: area; bar; bubble; bullet; candlestick; column; donut; funnel; line; ohlc; pie; polarArea; polarLine; polarScatter; radarArea; radarColumn; radarLine; rangeArea; rangeBar; rangeColumn; scatter; scatterLine; waterfall; verticalArea; verticalBullet; verticalLine or verticalRangeArea.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * The chart series tooltip configuration options.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function tooltip($value) {
        return $this->setProperty('tooltip', $value);
    }

    /**
    * The verticalArea chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function verticalArea($value) {
        return $this->setProperty('verticalArea', $value);
    }

    /**
    * The verticalLine chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function verticalLine($value) {
        return $this->setProperty('verticalLine', $value);
    }

    /**
    * The verticalRangeArea chart series options. Accepts all values supported by the series option.
    * @param  $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function verticalRangeArea($value) {
        return $this->setProperty('verticalRangeArea', $value);
    }

    /**
    * Sets the visual option of the ChartSeriesDefaults.
    * A function that can be used to create a custom visual for the points. Applicable for bar, column, pie, donut, funnel, line, scatterLine, rangeBar, rangeColumn and waterfall series. The available argument fields are: rect - the kendo.geometry.Rect that defines where the visual should be rendered.; options - the point options.; createVisual - a function that can be used to get the default visual.; category - the point category.; dataItem - the point dataItem.; value - the point value.; stackValue - the cumulative point value on the stack. Available only for stackable series.; sender - the chart instance.; series - the point series.; percentage - the point value represented as a percentage value. Available only for donut, pie and 100% stacked charts.; runningTotal - the sum of point values since the last "runningTotal" summary point. Available for waterfall series.; total - the sum of all previous series values. Available for waterfall series.; radius - the segment radius. Available for donut and pie series.; innerRadius - the segment inner radius. Available for donut series.; startAngle - the segment start angle. Available for donut and pie series.; endAngle - the segment end angle. Available for donut and pie series.; center - the segment center point. Available for donut and pie series. or points - the segment points. Available for funnel, line and scatterLine series..
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

    /**
    * The seriesDefaults notes configuration.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsNotes|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaults
    */
    public function notes($value) {
        return $this->setProperty('notes', $value);
    }

//<< Properties
}

?>
