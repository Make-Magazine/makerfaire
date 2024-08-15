<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesItemTargetBorder extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The color of the border.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemTargetBorder
    */
    public function color($value) {
        return $this->setProperty('color', $value);
    }

    /**
    * The following dash types are supported: "dash" - a line consisting of dashes; "dashDot" - a line consisting of a repeating pattern of dash-dot; "dot" - a line consisting of dots; "longDash" - a line consisting of a repeating pattern of long-dash; "longDashDot" - a line consisting of a repeating pattern of long-dash-dot; "longDashDotDot" - a line consisting of a repeating pattern of long-dash-dot-dot or "solid" - a solid line.
    * @param string|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemTargetBorder
    */
    public function dashType($value) {
        return $this->setProperty('dashType', $value);
    }

    /**
    * The width of the border in pixels. By default the border width is set to zero which means that the border will not appear.
    * @param float|\Kendo\JavaScriptFunction $value
    * @return \Kendo\Dataviz\UI\ChartSeriesItemTargetBorder
    */
    public function width($value) {
        return $this->setProperty('width', $value);
    }

//<< Properties
}

?>
