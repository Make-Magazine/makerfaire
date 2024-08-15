<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesItemLabels extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The label alignment when series.type is set to "donut", "funnel" or "pie".The supported values  for "donut" and "pie" are: "circle" - the labels are positioned in circle around the chart. or "column" - the labels are positioned in columns to the left and right of the chart.. The supported values for "funnel" are: "center" - the labels are positioned in the center over the funnel segment.; "right" - the labels are positioned on the right side of the chart and do not (if there is enough space) overlap the funnel segment(s). or "left" - the labels are positioned on the left side of the chart and do not (if there is enough space) overlap the funnel segment(s)..
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function align($value) {
        return $this->setProperty('align', $value);
    }

    /**
    * The background color of the labels. Accepts a valid CSS color string, including hex and rgb.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function background($value) {
        return $this->setProperty('background', $value);
    }

    /**
    * The border of the labels.
    * @param \Kendo\Dataviz\UI\ChartSeriesItemLabelsBorder|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function border($value) {
        return $this->setProperty('border', $value);
    }

    /**
    * The text color of the labels. Accepts a valid CSS color string, including hex and rgb.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The distance of the labels when series.type is set to "donut" or "pie".
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function distance($value) {
        return $this->setProperty('distance', $value);
    }

    /**
    * The font style of the labels. Accepts a valid CSS color string, for example "20px 'Courier New'".
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function font($value) {
        return $this->setProperty('font', $value);
    }

    /**
    * The format of the labels. Uses kendo.format.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function format($value) {
        return $this->setProperty('format', $value);
    }

    /**
    * The margin of the labels. A numeric value will set all margins.
    * @param float|\Kendo\Dataviz\UI\ChartSeriesItemLabelsMargin|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function margin($value) {
        return $this->setProperty('margin', $value);
    }

    /**
    * The padding of the labels. A numeric value will set all paddings.
    * @param float|\Kendo\Dataviz\UI\ChartSeriesItemLabelsPadding|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function padding($value) {
        return $this->setProperty('padding', $value);
    }

    /**
    * The position of the labels. "above" - the label is positioned at the top of the marker. Applicable for series that render points, incl. bubble.; "below" - the label is positioned at the bottom of the marker. Applicable for series that render points, incl. bubble.; "center" - the label is positioned at the point center. Applicable for bar, column, donut, pie, funnel, radarColumn and waterfall series.; "insideBase" - the label is positioned inside, near the base of the bar. Applicable for bar, column and waterfall series.; "insideEnd" - the label is positioned inside, near the end of the point. Applicable for bar, column, donut, pie, radarColumn and waterfall series.; "left" - the label is positioned to the left of the marker. Applicable for series that render points, incl. bubble.; "outsideEnd" - the label is positioned outside, near the end of the point. Applicable for bar, column, donut, pie, radarColumn and waterfall series. Not applicable for stacked series.; "right" - the label is positioned to the right of the marker. Applicable for series that render points, incl. bubble.; "top" - the label is positioned at the top of the segment. Applicable for funnel series.; "bottom" - the label is positioned at the bottom of the segment. Applicable for funnel series. or "auto" - the from and to labels area positioned at the top/bottom(rangeArea series) or left/right(verticalRangeArea series) so that they are outside the filled area. Applicable for rangeArea and verticalRangeArea series..
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function position($value) {
        return $this->setProperty('position', $value);
    }

    /**
    * The rotation angle of the labels. By default, the labels are not rotated.
    * @param string|float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function rotation($value) {
        return $this->setProperty('rotation', $value);
    }

    /**
    * Sets the template option of the ChartSeriesItemLabels.
    * The template which renders the chart series label.The fields which can be used in the template are: category - the category name. Available for area, bar, column, bubble, donut, line, pie and waterfall series.; dataItem - the original data item used to construct the point. Will be null if binding to array.; percentage - the point value represented as a percentage value. Available only for donut, pie and 100% stacked charts.; series - the data series; stackValue - the cumulative point value on the stack. Available only for stackable series.; value - the point value. Can be a number or object containing each bound field.; runningTotal - the sum of point values since the last "runningTotal" summary point. Available for waterfall series. or total - the sum of all previous series values. Available for waterfall series..
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the ChartSeriesItemLabels.
    * The template which renders the chart series label.The fields which can be used in the template are: category - the category name. Available for area, bar, column, bubble, donut, line, pie and waterfall series.; dataItem - the original data item used to construct the point. Will be null if binding to array.; percentage - the point value represented as a percentage value. Available only for donut, pie and 100% stacked charts.; series - the data series; stackValue - the cumulative point value on the stack. Available only for stackable series.; value - the point value. Can be a number or object containing each bound field.; runningTotal - the sum of point values since the last "runningTotal" summary point. Available for waterfall series. or total - the sum of all previous series values. Available for waterfall series..
    * @param string $value The template content.
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * If set to true the chart will display the series labels. By default chart series labels are not displayed.
    * @param boolean|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

    /**
    * Sets the visual option of the ChartSeriesItemLabels.
    * A function that can be used to create a custom visual for the labels. The available argument fields are: text - the label text.; rect - the kendo.geometry.Rect that defines where the visual should be rendered.; options - the label options.; createVisual - a function that can be used to get the default visual.; sender - the chart instance (may be undefined).; value - The point value.; category - The point category.; stackValue - The cumulative point value on the stack. Available only for the stackable series.; dataItem - The point dataItem.; series - The point series.; percentage - The point value that is represented as a percentage value. Available only for the Donut, Pie, and 100% stacked charts.; runningTotal - The sum of point values from the last runningTotal summary point onwards. Available for the Waterfall series. or total - The sum of all previous series values. Available for the Waterfall series..
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

    /**
    * The chart series from label configuration.
    * @param \Kendo\Dataviz\UI\ChartSeriesItemLabelsFrom|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function from($value) {
        return $this->setProperty('from', $value);
    }

    /**
    * The chart series to label configuration.
    * @param \Kendo\Dataviz\UI\ChartSeriesItemLabelsTo|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemLabels
    */
    public function to($value) {
        return $this->setProperty('to', $value);
    }

//<< Properties
}

?>
