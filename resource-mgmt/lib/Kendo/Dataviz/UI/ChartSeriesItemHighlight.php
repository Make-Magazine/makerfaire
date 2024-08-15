<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesItemHighlight extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The border of the highlighted chart series. The color is computed automatically from the base point color.
    * @param \Kendo\Dataviz\UI\ChartSeriesItemHighlightBorder|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemHighlight
    */
    public function border($value) {
        return $this->setProperty('border', $value);
    }

    /**
    * The highlight color. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemHighlight
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The line of the highlighted chart series. The color is computed automatically from the base point color.
    * @param \Kendo\Dataviz\UI\ChartSeriesItemHighlightLine|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemHighlight
    */
    public function line($value) {
        return $this->setProperty('line', $value);
    }

    /**
    * The opacity of the highlighted points.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemHighlight
    */
    public function opacity($value) {
        return $this->setProperty('opacity', $value);
    }

    /**
    * Sets the toggle option of the ChartSeriesItemHighlight.
    * A function that can be used to handle toggling the points highlight. The available argument fields are: preventDefault - a function that can be used to prevent showing the default highlight overlay.; show - a boolean value indicating whether the highlight should be shown.; visual - the visual element that should be highlighted.; category - the point category.; dataItem - the point dataItem.; value - the point value. or series - the point series..
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\ChartSeriesItemHighlight
    */
    public function toggle($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('toggle', $value);
    }

    /**
    * If set to true the chart will highlight the series when the user hovers it with the mouse. By default chart series highlighting is enabled.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemHighlight
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

    /**
    * Sets the visual option of the ChartSeriesItemHighlight.
    * A function that can be used to set custom visual for the point highlight.The available argument fields are: createVisual - a function that can be used to get the default highlight visual.; rect - the kendo.geometry.Rect that defines where the visual should be rendered.; visual - the visual element that should be highlighted.; options - the point options.; category - the point category.; dataItem - the point dataItem.; value - the point value.; sender - the chart instance.; series - the point series.; stackValue - the cumulative point value on the stack. Available only for stackable series.; percentage - the point value represented as a percentage value. Available only for donut, pie and 100% stacked charts.; runningTotal - the sum of point values since the last "runningTotal" summary point. Available for waterfall series.; total - the sum of all previous series values. Available for waterfall series.; from - the "from" point highlight visual options. Available for "rangeArea" and "verticalRangeArea" series. or to - the "to" point highlight visual options. Available for "rangeArea" and "verticalRangeArea" series..
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\ChartSeriesItemHighlight
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

//<< Properties
}

?>
