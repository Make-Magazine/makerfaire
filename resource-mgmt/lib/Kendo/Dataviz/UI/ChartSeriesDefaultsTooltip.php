<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesDefaultsTooltip extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The background color of the tooltip. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function background($value) {
        return $this->setProperty('background', $value);
    }

    /**
    * The border configuration options.
    * @param \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltipBorder|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function border($value) {
        return $this->setProperty('border', $value);
    }

    /**
    * The text color of the tooltip. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The tooltip font.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function font($value) {
        return $this->setProperty('font', $value);
    }

    /**
    * The format of the labels. Uses kendo.format.Format placeholders: Area, bar, column, funnel, line and pie{0} - value; Bubble{0} - x value{1} - y value{2} - size value{3} - category name; Scatter and scatterLine{0} - x value{1} - y value; Candlestick and OHLC{0} - open value{1} - high value{2} - low value{3} - close value{4} - category name or RangeArea, rangeBar, rangeColumn{0} - from value{1} - to value.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * The padding of the tooltip. A numeric value will set all paddings.
    * @param float|\Kendo\Dataviz\UI\ChartSeriesDefaultsTooltipPadding|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function padding($value) {
        return $this->setProperty('padding', $value);
    }

    /**
    * Sets the template option of the ChartSeriesDefaultsTooltip.
    * The template which renders the tooltip.The fields which can be used in the template are: category - the category name; dataItem - the original data item used to construct the point. Will be null if binding to array.; series - the data series; value - the point value (either a number or an object); runningTotal - the sum of point values since the last "runningTotal" summary point. Available for waterfall series. or total - the sum of all previous series values. Available for waterfall series..
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the ChartSeriesDefaultsTooltip.
    * The template which renders the tooltip.The fields which can be used in the template are: category - the category name; dataItem - the original data item used to construct the point. Will be null if binding to array.; series - the data series; value - the point value (either a number or an object); runningTotal - the sum of point values since the last "runningTotal" summary point. Available for waterfall series. or total - the sum of all previous series values. Available for waterfall series..
    * @param string $value The template content.
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * If set to true the chart will display the series tooltip. By default the series tooltip is not displayed.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsTooltip
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

//<< Properties
}

?>
