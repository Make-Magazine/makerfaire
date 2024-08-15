<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesItemMarkers extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The background color of the series markers.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function background($value) {
        return $this->setProperty('background', $value);
    }

    /**
    * The border of the markers.
    * @param \Kendo\JavaScriptFunction|\Kendo\Dataviz\UI\ChartSeriesItemMarkersBorder|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function border($value) {
        return $this->setProperty('border', $value);
    }

    /**
    * The chart series marker configuration for the "from" point. Supported for "rangeArea" and "verticalRangeArea" series.
    * @param \Kendo\Dataviz\UI\ChartSeriesItemMarkersFrom|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function from($value) {
        return $this->setProperty('from', $value);
    }

    /**
    * The marker size in pixels.
    * @param float|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function size($value) {
        return $this->setProperty('size', $value);
    }

    /**
    * The chart series marker configuration for the "to" point. Supported for "rangeArea" and "verticalRangeArea" series.
    * @param \Kendo\Dataviz\UI\ChartSeriesItemMarkersTo|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function to($value) {
        return $this->setProperty('to', $value);
    }

    /**
    * The markers shape.The supported values are: * "circle" - the marker shape is circle. * "square" - the marker shape is square. * "triangle" - the marker shape is triangle. * "cross" - the marker shape is cross.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function type($value) {
        return $this->setProperty('type', $value);
    }

    /**
    * If set to true the chart will display the series markers. By default chart series markers are displayed.
    * @param boolean|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function visible($value) {
        return $this->setProperty('visible', $value);
    }

    /**
    * Sets the visual option of the ChartSeriesItemMarkers.
    * A function that can be used to create a custom visual for the markers. The available argument fields are: rect - the kendo.geometry.Rect that defines where the visual should be rendered.; options - the marker options.; createVisual - a function that can be used to get the default visual.; category - the category of the marker point.; dataItem - the dataItem of the marker point.; value - the value of the marker point.; sender - the chart instance. or series - the series of the marker point..
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

    /**
    * The rotation angle of the markers.
    * @param float|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemMarkers
    */
    public function rotation($value) {
        return $this->setProperty('rotation', $value);
    }

//<< Properties
}

?>
