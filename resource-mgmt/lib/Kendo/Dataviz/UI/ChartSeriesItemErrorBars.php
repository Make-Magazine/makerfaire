<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesItemErrorBars extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The error bars value.The following value types are supported: "stderr" - the standard error of the series values will be used to calculate the point low and high value; "stddev(n)" - the standard deviation of the series values will be used to calculate the point low and high value. A number can be specified between the parentheses, that will be multiplied by the calculated standard deviation.; "percentage(n)" - a percentage of the point value; A number that will be subtracted/added to the point value; An array that holds the low and high difference from the point value or A function that returns the errorBars point value.
    * @param string|float|array|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBars
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * Sets the visual option of the ChartSeriesItemErrorBars.
    * A function that can be used to create a custom visual for the error bars. The available argument fields are: rect - the kendo.geometry.Rect that defines where the visual should be rendered.; options - the error bar options.; createVisual - a function that can be used to get the default visual.; low - the error bar low value.; high - the error bar high value. or sender - the chart instance..
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBars
    */
    public function visual($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('visual', $value);
    }

    /**
    * The xAxis error bars value. See the series.errorBars.value option for a list of the supported value types.
    * @param string|float|array|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBars
    */
    public function xValue($value) {
        return $this->setProperty('xValue', $value);
    }

    /**
    * The yAxis error bars value. See the series.errorBars.value option for a list of the supported value types.
    * @param string|float|array|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBars
    */
    public function yValue($value) {
        return $this->setProperty('yValue', $value);
    }

    /**
    * If set to false, the error bars caps will not be displayed. By default the caps are visible.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBars
    */
    public function endCaps($value) {
        return $this->setProperty('endCaps', $value);
    }

    /**
    * The color of the error bars. Accepts a valid CSS color string, including hex and rgb.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBars
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The error bars line options.
    * @param \Kendo\Dataviz\UI\ChartSeriesItemErrorBarsLine|array $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBars
    */
    public function line($value) {
        return $this->setProperty('line', $value);
    }

//<< Properties
}

?>
