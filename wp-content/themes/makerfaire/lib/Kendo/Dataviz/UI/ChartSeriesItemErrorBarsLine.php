<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesItemErrorBarsLine extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The width of the line.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBarsLine
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

    /**
    * The dash type of the error bars line.The following dash types are supported: "dash" - a line consisting of dashes; "dashDot" - a line consisting of a repeating pattern of dash-dot; "dot" - a line consisting of dots; "longDash" - a line consisting of a repeating pattern of long-dash; "longDashDot" - a line consisting of a repeating pattern of long-dash-dot; "longDashDotDot" - a line consisting of a repeating pattern of long-dash-dot-dot or "solid" - a solid line.
    * @param string $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemErrorBarsLine
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

//<< Properties
}

?>
